# Docker Context Reproducer

Installation

1. clone this repo
2. Build the docker image, then run it:
   ```
   make run
   ```
2. Build a vagrant box (first time only):
   ```
   make vagrant-build
   ```
4. Inside the VM: Build the docker image, then run it:
   ```
   make vagrant-run
   ```

On my computer, it displays:

* make run: `fc2733a0499720a56fccebde6a9dc261`
* make vagrant-run: `69f8bdb4ae639a1616eaea4e9b42e198`
