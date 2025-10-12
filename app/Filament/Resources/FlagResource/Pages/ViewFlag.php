<?php

namespace App\Filament\Resources\FlagResource\Pages;

use App\Filament\Resources\FlagResource;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Grid;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use App\Notifications\FlagResolvedNotification;
use App\Notifications\ContentRemovedNotification;
use Filament\Infolists\Components\ExtraEntry;

class ViewFlag extends ViewRecord
{
    protected static string $resource = FlagResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Flag Details')
                    ->schema([
                        TextEntry::make('flaggable_type')
                            ->label('Content Type')
                            ->formatStateUsing(fn($state) => match ($state) {
                                'App\\Models\\Post' => 'Post',
                                'App\\Models\\Comment' => 'Comment',
                                'App\\Models\\User' => 'User',
                                'App\\Models\\Image' => 'Image',
                                default => class_basename($state)
                            }),

                        TextEntry::make('reason'),

                        TextEntry::make('flaggedBy.name')
                            ->label('Reported By'),

                        TextEntry::make('status')
                            ->badge()
                            ->color(fn($state) => match ($state) {
                                'pending' => 'warning',
                                'reviewed' => 'info',
                                'resolved' => 'success',
                                default => 'gray'
                            }),

                        TextEntry::make('created_at')
                            ->label('Reported At')
                            ->dateTime(),
                    ])
                    ->columns(2),

                Section::make('Flagged Content')
                    ->schema(function ($record) {
                        $flaggable = $record->flaggable;
                        if (!$flaggable) {
                            return [
                                TextEntry::make('')->label('Content has been deleted')
                            ];
                        }

                        // Determine the current state and if the model supports hiding
                        $isHidden = $flaggable->is_hidden;

                        $schema = [];

                        $schema[] = TextEntry::make('hidden_status')
                                ->label('Visibility Status')
                                ->badge()
                                ->state($isHidden ? 'Hidden' : 'Visible')
                                ->color($isHidden ? 'danger' : 'success');


                        // 3. FLAGGED CONTENT DETAILS
                        $contentDetails = match (get_class($flaggable)) {
                            'App\\Models\\Post' => [
                                TextEntry::make('flaggable.title')
                                    ->label('Post Title'),
                                TextEntry::make('flaggable.content')
                                    ->label('Post Content')
                                    ->html()
                                    ->columnSpanFull(),
                            ],
                            'App\\Models\\Comment' => [
                                TextEntry::make('flaggable.content')
                                    ->label('Comment Content')
                                    ->html()
                                    ->columnSpanFull(),
                                TextEntry::make('flaggable.post.title')
                                    ->label('On Post'),
                            ],
                            'App\\Models\\User' => [
                                TextEntry::make('flaggable.name')
                                    ->label('User Name'),
                                TextEntry::make('flaggable.email')
                                    ->label('Email'),
                            ],
                            default => [
                                TextEntry::make('')->label('Content type: ' . class_basename($flaggable))
                            ]
                        };

                        // Merge the status badge/banner with the content details
                        return array_merge($schema, $contentDetails);
                    })
                    ->columns(2),

                Section::make('Resolution Details')
                    ->schema([
                        TextEntry::make('admin_notes')
                            ->label('Admin Notes')
                            ->columnSpanFull()
                            ->placeholder('No notes added'),

                        TextEntry::make('resolved_at')
                            ->label('Resolved At')
                            ->dateTime()
                            ->placeholder('Not resolved'),

                        TextEntry::make('resolvedBy.name')
                            ->label('Resolved By')
                            ->placeholder('Not resolved'),
                    ])
                    ->columns(2)
                    ->hidden(fn($record) => $record->status !== 'resolved'),
            ]);
    }

    protected function getHeaderActions(): array
    {
        $record = $this->getRecord();
        $flaggable = $record->flaggable;

        $supportsHiding = $flaggable;
        $isHidden = $supportsHiding ? $flaggable->is_hidden : false;

        return [
            // MODIFIED EDIT ACTION: Simplified form for notes and status updates
            Actions\EditAction::make()
                ->form([
                    Select::make('status')
                        ->options([
                            'pending' => 'Pending',
                            'reviewed' => 'Reviewed',
                            'resolved' => 'Resolved',
                        ])
                        ->required(),

                    Textarea::make('admin_notes')
                        ->label('Admin Notes')
                        ->placeholder('Add or update notes about the resolution...')
                        ->columnSpanFull()
                        ->rows(5),
                ]),

            // TOGGLE ACTION: Primary action for content visibility and initial flag resolution
            Actions\Action::make('toggleVisibility')
                // Dynamic UI based on the current state
                ->label($isHidden ? 'Make Visible' : 'Hide Content')
                ->icon($isHidden ? 'heroicon-o-eye' : 'heroicon-o-eye-slash')
                ->color($isHidden ? 'success' : 'danger')
                ->requiresConfirmation()
                ->action(function () use ($record, $flaggable, $isHidden) {

                    // Determine the new state (toggle it)
                    $newState = !$isHidden;
                    $actionName = $newState ? 'Hidden' : 'Made Visible';

                    if ($flaggable) {
                        // 1. Update the is_hidden field on the related model
                        $flaggable->update(['is_hidden' => $newState]);

                        // 2. Update the flag status (mark as resolved)
                        $record->update([
                            'status' => 'resolved',
                            'resolved_at' => now(),
                            'resolved_by' => Auth::id(),
                        ]);

                        // 3. GET USERS FOR NOTIFICATIONS
                        $reporter = $record->flaggedBy;
                        $contentOwner = null;

                        // Attempt to find the content owner (assuming 'user' or 'creator' relation)
                        if (method_exists($flaggable, 'user')) {
                            $contentOwner = $flaggable->user;
                        } elseif (method_exists($flaggable, 'creator')) {
                            $contentOwner = $flaggable->creator;
                        }

                        // --- Notification 1: To the Reporter ---
                        if ($reporter) {
                            $reporter->notify(new FlagResolvedNotification($record));
                        }

                        // --- Notification 2: To the Content Owner (Only if hidden) ---
                        if ($newState === true && $contentOwner && $contentOwner->id !== $reporter->id) {
                            // Send removal notice only if content was hidden
                            $contentOwner->notify(new ContentRemovedNotification($flaggable, $record->reason));
                        }


                        Notification::make()
                            ->title('Content Status Updated')
                            ->body("The content has been successfully {$actionName} and the flag resolved.")
                            ->success()
                            ->send();

                        // Redirect back to refresh the page state
                        return redirect()->route('filament.admin.resources.flags.view', $record);
                    } else {
                        Notification::make()
                            ->title('Action Failed')
                            ->body('The content is missing or does not support visibility toggling.')
                            ->danger()
                            ->send();
                    }
                })
                // Only show the action if the content exists and has the is_hidden property
                ->visible(fn() => $supportsHiding),

            Actions\DeleteAction::make(),
        ];
    }
}
