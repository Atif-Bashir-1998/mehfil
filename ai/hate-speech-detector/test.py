from src.utils import preprocess_and_tokenize

# Example 1: Regular text
text = "Hello! This is a sample tweet with @user and #hashtag 😊"
tokens = preprocess_and_tokenize(text)
print(tokens)
# Output: ['hello', 'sampl', 'tweet', 'hashtag']

# Example 2: Social media text with URLs and mentions
text2 = "RT @user: Check out this amazing website! https://example.com #awesome 😍"
tokens2 = preprocess_and_tokenize(text2)
print(tokens2)
# Output: ['check', 'amaz', 'websit', 'awesom']

# Example 3: Text with numbers and special characters
text3 = "The price is $100.50 for 2 items! Special discount: 25% off."
tokens3 = preprocess_and_tokenize(text3)
print(tokens3)
# Output: ['price', '10050', '2', 'item', 'special', 'discount', '25']
