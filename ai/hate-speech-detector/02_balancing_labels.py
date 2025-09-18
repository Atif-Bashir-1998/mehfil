import pandas as pd
import numpy as np
import random
from sklearn.utils import resample
import os

# Set random seeds for reproducibility
random.seed(42)
np.random.seed(42)

def augment_text(text, num_augmentations=1):
    """
    Simple text augmentation by shuffling words and adding minor variations
    """
    augmented_texts = []
    words = text.split()

    if len(words) <= 1:  # Can't augment single-word tweets
        return [text]

    for _ in range(num_augmentations):
        # Shuffle words (but keep original as one option)
        shuffled_words = words.copy()
        random.shuffle(shuffled_words)
        augmented_text = ' '.join(shuffled_words)

        # Add some simple variations
        variations = [
            augmented_text,
            augmented_text + "!",
            augmented_text.lower(),
            augmented_text.upper()
        ]

        augmented_texts.extend(variations)

    return list(set(augmented_texts))  # Remove duplicates

def balance_dataset(df, target_size=25000, random_state=42):
    """
    Balance the dataset by downsampling majority class and augmenting minority class
    """
    # Separate classes
    df_not_hate = df[df['label'] == 0]
    df_hate = df[df['label'] == 1]

    print(f"Original counts - Not Hate: {len(df_not_hate)}, Hate: {len(df_hate)}")

    # Downsample majority class (Not Hate)
    df_not_hate_downsampled = resample(df_not_hate,
                                      replace=False,  # Without replacement
                                      n_samples=target_size,
                                      random_state=random_state)

    # Calculate how many augmented samples we need for hate class
    current_hate_count = len(df_hate)
    needed_augmentations = target_size - current_hate_count

    print(f"We need to create {needed_augmentations} augmented hate samples")

    # Augment hate speech samples
    augmented_hate_samples = []

    for _, row in df_hate.iterrows():
        if len(augmented_hate_samples) >= needed_augmentations:
            break

        # Get augmentations for this tweet
        augmentations = augment_text(row['tweet'], num_augmentations=2)

        for aug_text in augmentations:
            if len(augmented_hate_samples) < needed_augmentations:
                new_sample = {
                    'id': f"aug_{row['id']}_{len(augmented_hate_samples)}",
                    'tweet': aug_text,
                    'label': 1
                }
                augmented_hate_samples.append(new_sample)

    # Create DataFrame from augmented samples
    df_augmented_hate = pd.DataFrame(augmented_hate_samples)

    # Combine original hate samples with augmented ones
    df_hate_balanced = pd.concat([df_hate, df_augmented_hate], ignore_index=True)

    # If we still don't have enough, duplicate some randomly
    if len(df_hate_balanced) < target_size:
        additional_needed = target_size - len(df_hate_balanced)
        additional_samples = df_hate_balanced.sample(n=additional_needed,
                                                   replace=True,
                                                   random_state=random_state)
        df_hate_balanced = pd.concat([df_hate_balanced, additional_samples], ignore_index=True)

    # Combine both balanced classes
    balanced_df = pd.concat([df_not_hate_downsampled, df_hate_balanced], ignore_index=True)

    # Shuffle the final dataset
    balanced_df = balanced_df.sample(frac=1, random_state=random_state).reset_index(drop=True)

    return balanced_df

def main():
    # Load the data
    train_df = pd.read_csv('./src/data/train.csv')

    # Check current distribution
    print("Original class distribution:")
    print(train_df['label'].value_counts())

    # Balance the dataset
    balanced_df = balance_dataset(train_df, target_size=25000)

    # Check new distribution
    print("\nBalanced class distribution:")
    print(balanced_df['label'].value_counts())

    # Save the balanced dataset
    os.makedirs('./src/data/balanced', exist_ok=True)
    balanced_df.to_csv('./src/data/balanced/train_balanced.csv', index=False)

    print(f"\nBalanced dataset saved with {len(balanced_df)} samples")
    print("Dataset saved to: ./src/data/balanced/train_balanced.csv")

if __name__ == "__main__":
    main()
