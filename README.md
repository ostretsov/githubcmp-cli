# githubcmp-cli

Command line interface for githubcmp library

```
$ ./github help cmp
```

Just an example of how it works:

```
$ date
Tue Jan  5 07:56:19 MSK 2016
$ ./github cmp --token=yourgithubtoken
1. Please enter the name (username/repository) of repository on github.com: symfony/symfony
Getting repository information...
2. Please enter the name (username/repository) of repository on github.com: zendframework/zf2
Getting repository information...
Add one more repository to compare? [y/n] y
3. Please enter the name (username/repository) of repository on github.com: bcit-ci/CodeIgniter
Getting repository information...
Add one more repository to compare? [y/n] y
4. Please enter the name (username/repository) of repository on github.com: cakephp/cakephp
Getting repository information...
Add one more repository to compare? [y/n] y
5. Please enter the name (username/repository) of repository on github.com: laravel/laravel
Getting repository information...
Add one more repository to compare? [y/n] y
6. Please enter the name (username/repository) of repository on github.com: yiisoft/yii2
Getting repository information...
Add one more repository to compare? [y/n] n

1. symfony/symfony with 22%

+-----------------------+-------+--------+
| Key                   | Value | Factor |
+-----------------------+-------+--------+
| size                  | 98059 | 0.2    |
| stargazersCount       | 11422 | 0.7    |
| forks                 | 4511  | 1      |
| openIssues            | 907   | 0.2    |
| subscribersCount      | 1032  | 1      |
| userPublicRepos       | 78    | 0.2    |
| commitsCount          | 2124  | 0.5    |
| commitsLastMonthCount | 94    | 0.8    |
| avgCommitsPerWeek     | 40    | 2      |
| contributorsCount     | 1550  | 1      |
+-----------------------+-------+--------+
Absolute rating: 36114.40

2. zendframework/zf2 with 19%

+-----------------------+--------+--------+
| Key                   | Value  | Factor |
+-----------------------+--------+--------+
| size                  | 134287 | 0.2    |
| stargazersCount       | 5290   | 0.7    |
| forks                 | 3216   | 1      |
| openIssues            | 466    | 0.2    |
| subscribersCount      | 619    | 1      |
| userPublicRepos       | 123    | 0.2    |
| commitsCount          | 617    | 0.5    |
| commitsLastMonthCount | 0      | 0.8    |
| avgCommitsPerWeek     | 11     | 2      |
| contributorsCount     | 996    | 1      |
+-----------------------+--------+--------+
Absolute rating: 35839.70

3. bcit-ci/CodeIgniter with 17%

+-----------------------+-------+--------+
| Key                   | Value | Factor |
+-----------------------+-------+--------+
| size                  | 84810 | 0.2    |
| stargazersCount       | 11375 | 0.7    |
| forks                 | 5851  | 1      |
| openIssues            | 75    | 0.2    |
| subscribersCount      | 1520  | 1      |
| userPublicRepos       | 5     | 0.2    |
| commitsCount          | 713   | 0.5    |
| commitsLastMonthCount | 10    | 0.8    |
| avgCommitsPerWeek     | 13    | 2      |
| contributorsCount     | 467   | 1      |
+-----------------------+-------+--------+
Absolute rating: 33169.00

4. cakephp/cakephp with 15%

+-----------------------+-------+--------+
| Key                   | Value | Factor |
+-----------------------+-------+--------+
| size                  | 98850 | 0.2    |
| stargazersCount       | 6215  | 0.7    |
| forks                 | 2845  | 1      |
| openIssues            | 165   | 0.2    |
| subscribersCount      | 629   | 1      |
| userPublicRepos       | 47    | 0.2    |
| commitsCount          | 2086  | 0.5    |
| commitsLastMonthCount | 113   | 0.8    |
| avgCommitsPerWeek     | 40    | 2      |
| contributorsCount     | 499   | 1      |
+-----------------------+-------+--------+
Absolute rating: 29349.30

5. laravel/laravel with 15%

+-----------------------+-------+--------+
| Key                   | Value | Factor |
+-----------------------+-------+--------+
| size                  | 22086 | 0.2    |
| stargazersCount       | 20842 | 0.7    |
| forks                 | 6752  | 1      |
| openIssues            | 28    | 0.2    |
| subscribersCount      | 2825  | 1      |
| userPublicRepos       | 20    | 0.2    |
| commitsCount          | 263   | 0.5    |
| commitsLastMonthCount | 15    | 0.8    |
| avgCommitsPerWeek     | 5     | 2      |
| contributorsCount     | 370   | 1      |
+-----------------------+-------+--------+
Absolute rating: 29116.70

6. yiisoft/yii2 with 12%

+-----------------------+-------+--------+
| Key                   | Value | Factor |
+-----------------------+-------+--------+
| size                  | 52466 | 0.2    |
| stargazersCount       | 7257  | 0.7    |
| forks                 | 4297  | 1      |
| openIssues            | 1049  | 0.2    |
| subscribersCount      | 1126  | 1      |
| userPublicRepos       | 29    | 0.2    |
| commitsCount          | 1885  | 0.5    |
| commitsLastMonthCount | 79    | 0.8    |
| avgCommitsPerWeek     | 36    | 2      |
| contributorsCount     | 656   | 1      |
+-----------------------+-------+--------+
Absolute rating: 22945.40

The number of requests remaining in the current rate limit window: 4806.
```

## TODO
* make factor values configurable;
* add docker support;
* travis;
* README.md.