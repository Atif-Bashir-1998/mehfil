FROM continuumio/miniconda3:4.12.0

WORKDIR /app

COPY environment.yml .

RUN conda env create -f environment.yml

# Initialize conda and activate environment
RUN echo "source activate flask-hate-speech-api" > ~/.bashrc
ENV PATH /opt/conda/envs/flask-hate-speech-api/bin:$PATH

# Download NLTK data
RUN python -m nltk.downloader punkt stopwords wordnet

COPY . .

EXPOSE 5000

CMD ["python3", "api.py"]
