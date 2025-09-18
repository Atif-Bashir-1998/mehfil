# HATE SPEECH DETECTOR

### Datasets downloaded from
- [Hate Speech & Offensive Language Dataset](https://www.kaggle.com/datasets/mrmorj/hate-speech-and-offensive-language-dataset/data)
- [Twitter Sentiment Analysis Hatred Speech](https://www.kaggle.com/datasets/arkhoshghalb/twitter-sentiment-analysis-hatred-speech/data)

- Twitter Sentiment Analysis Hatred Speech: label 1 means it is hate speech

### Thought process
1- Step one was to have a dataset to train our ML model on
2- Datasets were obtained from kaggle
3- Unnecessary columns were dropped and the shape of both datasets was made consistent
4- Data was loaded as dataframe. Checked any missing and null values in the dataset
5- Drew graph to check if the data is equally distributed among the classes (hate-speech & not-hate-speech). Upon checking, the samples of normal text were larger than the hate speech text. So, we undersampled the normal class (i.e dropped samples randomly to equal the number of samples to hate speech)
6- Preprocessing was done to clean the sample text. Any symbols, hashtags and emojis were removed from the text. The text was later tokenized.
7- As a ML model can not directly work on text. We needed to represent each token in the sample into its vectoriezed form. For this we used Word2Vec model. This model was then used to convert the dataset into vectorized dataset.
7- Data was split into two sets. Training set and testing set
8- Three ML models were used to see their effectiveness. (logistic_regression, svm, random_forest). They were trained on the training dataset and later their performance was analyzed when they were given training dataset.
9- Logistic Regression model proved to be the most effective among all of them. So, the hate speech detector uses logistic regression

##### Merging of Datasets
The initial data set (Hate Speech & Offensive Language Dataset) did not have a lot of samples for hate speech. So, another dataset was concatinated.

### What we did?
1- downloaded the data from kaggle. [Twitter Sentiment Analysis Hatred Speech](https://www.kaggle.com/datasets/arkhoshghalb/twitter-sentiment-analysis-hatred-speech/data). The data was alread split into train.csv and test.csv.

2- loaded the data into a pandas dataframe for explatory analysis. Upon analysis it was noticed that there was a clear difference between the number of hate speech samples and normal speech samples, this could cause *class imbalance* problem. Secondly we tried to see if there is any relation between tweet length and its label AND the word count in a tweet and its label. These two parameters were nicely distributed but we had to somehow equate the number of samples in both the labels.

3- To fix the issues in step_2, we decided to downsample the majority class and augment the minority class. The target was to have 25K HateSpeech and 25K NotHateSpeech samples.

4- Now, we had to do some pre-processing on the training dataset. We created utility functions in `./utils/text_preprocessor.py`. The preprocessing involved **lowercasing** a given string, then **removing hashtags** while keeping the text, **removing punctuations and non-alphanumeric characters**. **Stemming** was also done and finally the given string was **tokenized** and returned from the function.

5- As a ML model, we can't directly give text inputs to the model. Only numbers can be the input. To convert the text into numbers, we used TF-IDF embeddings. The balanced training data was first preprocessed and tokenized by the helper functions and then using `TfidfVectorizer`, we converted each tweet into its numerical representation. The vectors, labels and vectorizer both were saved at `./src/data/tfidf_embeddings/`

['woman', 'king', 'man', 'queen']
['0.86', '0.55', '0.51', '0.88']


