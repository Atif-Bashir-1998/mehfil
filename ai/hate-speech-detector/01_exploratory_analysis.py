import pandas as pd
import matplotlib.pyplot as plt
import seaborn as sns
import os

# Create the images directory if it doesn't exist
os.makedirs('./images', exist_ok=True)

# Load the training data
train_df = pd.read_csv('./src/data/train.csv')

# Check the first few rows
print(train_df.head())

# Check the column names and data types
print(train_df.info())

# Get basic statistics for numerical columns
print(train_df.describe())

# Check for missing values in each column
print(train_df.isnull().sum())

# Check the distribution of labels (e.g., 0 = not hate speech, 1 = hate speech)
label_counts = train_df['label'].value_counts()
print(label_counts)

# Plot the distribution and save it
plt.figure(figsize=(8, 5))
sns.countplot(x='label', data=train_df)
plt.title('Distribution of Labels in Training Data')
plt.xlabel('Label (0:Not Hate, 1:Hate)')
plt.ylabel('Count')
plt.savefig('./images/label_distribution.png', dpi=300, bbox_inches='tight')
plt.close()  # Close the figure to free memory

# Create new features to analyze the text
train_df['tweet_length'] = train_df['tweet'].apply(len)
train_df['word_count'] = train_df['tweet'].apply(lambda x: len(x.split()))

# Compare tweet length for hate vs. non-hate speech and save it
plt.figure(figsize=(12, 5))
sns.histplot(data=train_df, x='tweet_length', hue='label', kde=True)
plt.title('Distribution of Tweet Length by Label')
plt.tight_layout()
plt.savefig('./images/tweet_length_distribution.png', dpi=300, bbox_inches='tight')
plt.close()

# 2. Word count distribution
plt.figure(figsize=(12, 5))
sns.histplot(data=train_df, x='word_count', hue='label', kde=True)
plt.title('Distribution of Word Count by Label')
plt.tight_layout()
plt.savefig('./images/word_count_distribution.png', dpi=300, bbox_inches='tight')
plt.close()

print("All plots have been saved to the ./images directory!")
