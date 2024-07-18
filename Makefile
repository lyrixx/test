help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'
.PHONY: help


run: ## Run docker container
	docker build -t test-cache . && docker run --rm test-cache ./checksum.sh

vagrant-build: ## Build vagrant box
	vagrant up

vagrant-run: ## Run vagrant box
	vagrant ssh -- 'cd /cache-reproducer && make run'
