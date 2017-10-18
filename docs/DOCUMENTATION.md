# Docs

Contains all details on how to use this (TBC).

## Usage

Create git-config.json file with the following structure:

```json
{
    "config": {
        "endpoint": "https://gitlab.com/api/v4/projects/<insert project id here>/variables/",
        "credentials": "super-secret-token"
    },
    "data": {
        "test": "kaboom",
        "kaboom": "test"
    }
}
```

Then run this docker command to view all available command:

```bash
docker run --rm -ti -v $PWD:/app travissouth/gitup list
```

To add config, run the docker command below:

```bash
docker run --rm -ti -v $PWD:/app travissouth/gitup add
```
