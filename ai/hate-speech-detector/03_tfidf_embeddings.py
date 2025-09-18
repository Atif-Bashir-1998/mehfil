import pandas as pd
import numpy as np
from sklearn.feature_extraction.text import TfidfVectorizer
import pickle
import os
from src.utils import preprocess_and_tokenize

# Load the balanced training data
print("Loading balanced training data...")
df = pd.read_csv('./src/data/balanced/train_balanced.csv')

# Preprocess all tweets
print("Preprocessing text data...")
processed_texts = []

for i, tweet in enumerate(df['tweet']):
    tokens = preprocess_and_tokenize(tweet)
    # Join tokens back into a string for TF-IDF
    processed_text = ' '.join(tokens)
    processed_texts.append(processed_text)

    # Show progress every 1000 tweets
    if (i + 1) % 1000 == 0:
        print(f"Processed {i + 1}/{len(df)} tweets")

print(f"Total documents processed: {len(processed_texts)}")

# Create TF-IDF vectorizer
print("Creating TF-IDF vectors...")
tfidf_vectorizer = TfidfVectorizer(
    max_features=5000,  # Keep top 5000 words
    min_df=2,           # Ignore words that appear in less than 2 documents
    max_df=0.8          # Ignore words that appear in more than 80% of documents
)

# Fit and transform the text data
X_tfidf = tfidf_vectorizer.fit_transform(processed_texts)

# Convert to dense array (if you have enough RAM)
# If you have memory issues, keep it as sparse matrix
X_dense = X_tfidf.toarray()

# Get the labels
y = df['label'].values

# Create directory if it doesn't exist
os.makedirs('./src/data/tfidf_embeddings', exist_ok=True)

# Save everything
print("Saving embeddings...")

# Save TF-IDF vectors
np.save('./src/data/tfidf_embeddings/tfidf_vectors.npy', X_dense)

# Save labels
np.save('./src/data/tfidf_embeddings/labels.npy', y)

# Save the vectorizer (so we can transform new text later)
with open('./src/data/tfidf_embeddings/tfidf_vectorizer.pkl', 'wb') as f:
    pickle.dump(tfidf_vectorizer, f)

print("All done!")
print(f"TF-IDF vectors shape: {X_dense.shape}")
print(f"Vocabulary size: {len(tfidf_vectorizer.vocabulary_)}")
print(f"Number of labels: {len(y)}")
