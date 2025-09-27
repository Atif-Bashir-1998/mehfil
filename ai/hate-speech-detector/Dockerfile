FROM continuumio/miniconda3:latest

WORKDIR /app

COPY environment.yml .
RUN conda env create -f environment.yml

SHELL ["conda", "run", "-n", "hate-speech-api", "/bin/bash", "-c"]

# Install ALL required NLTK data
RUN python -c "import nltk; nltk.download('punkt_tab'); nltk.download('punkt'); nltk.download('stopwords')"

COPY . .

# EXPOSE 8000
# ENV PORT=8000

CMD ["conda", "run", "-n", "hate-speech-api", "python", "app.py"]
