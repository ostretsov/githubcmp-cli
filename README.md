# githubcmp-cli

Command line interface for [githubcmp library](https://github.com/ostretsov/githubcmp).

```sh
$ ./github help cmp
```

Two or more repositories could be compared by running the following command:

```sh
$ ./github cmp --token=your_github_token --type=gist
```

By specifying `gist` type results will be published on [gist.github.com](https://gist.github.com/). Your token then must be with the gist scope!