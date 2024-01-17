all:

phar:
	phar-composer build .

buildimage:
	docker build -f Containerfile -t spojenet/fiobank-statement-downloader:latest .

buildx:
	docker buildx build -f Containerfile . --push --platform linux/arm/v7,linux/arm64/v8,linux/amd64 --tag spojenet/fiobank-statement-downloader:latest

drun:
	docker run --env-file .env spojenet/fiobank-statement-downloader:latest
