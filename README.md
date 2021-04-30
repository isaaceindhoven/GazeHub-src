# GazeHub
GazeHub is part of [Gaze](https://isaaceindhoven.github.io/GazeHub/docs).
GazeHub is the main server that is responsible for sending data from the backend to the frontend.

## Documentation
[Click here](https://isaaceindhoven.github.io/GazeHub/docs/#/gazehub) to go to the full documentation for Gaze and GazeHub.

## Installation
To install GazeHub go to the [GazeHub release repository](https://github.com/isaaceindhoven/GazeHub) and follow the instructions there.

## Development
For development you need [Composer](https://getcomposer.org/) or [Docksal](https://docksal.io/) installed on your system.

### Unit tests
To run the unit tests:

```bash
./vendor/bin/phpunit

# Or for docksal
fin phpunit
```

You can test all the supported PHP versions by running the `unittest.sh` script:

```bash
./unittest.sh
```

### Create phar
To create a phar-file that is executable:

```bash
php --define phar.readonly=0 create-archive.php
```

### Release
To release a new version of GazeHub a new phar file needs te be created in the [GazeHub release repository](https://github.com/isaaceindhoven/GazeHub). 
Follow the instructions there to create the new release.
