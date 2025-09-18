import pandas as pd
import numpy as np
from gensim.models import Word2Vec
import os
from src.utils import preprocess_and_tokenize

# Load the balanced training data
print("Loading balanced training data...")
df = pd.read_csv('./src/data/balanced/train_balanced.csv')

# Preprocess and tokenize all tweets (keep as lists of tokens for Word2Vec)
print("Preprocessing and tokenizing text data...")
tokenized_corpus = []

for i, tweet in enumerate(df['tweet']):
    tokens = preprocess_and_tokenize(tweet)
    tokenized_corpus.append(tokens)

    # Show progress every 1000 tweets
    if (i + 1) % 1000 == 0:
        print(f"Processed {i + 1}/{len(df)} tweets")

print(f"Total documents processed: {len(tokenized_corpus)}")

# Train Word2Vec model
print("Training Word2Vec model...")
word2vec_model = Word2Vec(
    sentences=tokenized_corpus,
    vector_size=100,    # Size of word vectors
    window=5,           # Context window size
    min_count=2,        # Ignore words that appear less than 2 times
    workers=4,          # Use 4 CPU cores
    epochs=10           # Number of training iterations
)

# Create document vectors by averaging word vectors
print("Creating document vectors...")
document_vectors = []

for tokens in tokenized_corpus:
    word_vectors = []
    for word in tokens:
        if word in word2vec_model.wv:  # Check if word is in vocabulary
            word_vectors.append(word2vec_model.wv[word])

    # Average the word vectors to get document vector
    if word_vectors:
        doc_vector = np.mean(word_vectors, axis=0)
    else:
        doc_vector = np.zeros(100)  # If no words, use zero vector

    document_vectors.append(doc_vector)

# Convert to numpy array
X_word2vec = np.array(document_vectors)

# Get the labels
y = df['label'].values

# Create directory if it doesn't exist
os.makedirs('./src/data/word2vec_embeddings', exist_ok=True)

# Save everything
print("Saving embeddings...")

# Save Word2Vec document vectors
np.save('./src/data/word2vec_embeddings/word2vec_vectors.npy', X_word2vec)

# Save labels
np.save('./src/data/word2vec_embeddings/labels.npy', y)

# Save the Word2Vec model
word2vec_model.save('./src/data/word2vec_embeddings/word2vec_model.model')

print("All done!")
print(f"Word2Vec vectors shape: {X_word2vec.shape}")
print(f"Vocabulary size: {len(word2vec_model.wv.key_to_index)}")
print(f"Number of labels: {len(y)}")
