# githubcmp-cli
Command line interface for githubcmp library

```
1. Please enter the URL of repository on github.com: symfony/symfony
2. Please enter the URL of repository on github.com: zendframework/zf2
Add one more repository to compare? [y/n] y
3. Please enter the URL of repository on github.com: bcit-ci/CodeIgniter
Add one more repository to compare? [y/n] y
4. Please enter the URL of repository on github.com: cakephp/cakephp
Add one more repository to compare? [y/n] y
5. Please enter the URL of repository on github.com: laravel/laravel
Add one more repository to compare? [y/n] y
6. Please enter the URL of repository on github.com: yiisoft/yii2
Add one more repository to compare? [y/n] n

1. symfony/symfony with 22%

+-----------------------+-------+--------+
| Key                   | Value | Factor |
+-----------------------+-------+--------+
| size                  | 97958 | 0.2    |
| stargazersCount       | 11416 | 0.7    |
| forks                 | 4506  | 1      |
| openIssues            | 913   | 0.2    |
| subscribersCount      | 1032  | 1      |
| userPublicRepos       | 78    | 0.2    |
| commitsCount          | 2120  | 0.5    |
| commitsLastMonthCount | 90    | 0.8    |
| avgCommitsPerWeek     | 40    | 2      |
| contributorsCount     | 1549  | 1      |
+-----------------------+-------+--------+

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

3. bcit-ci/CodeIgniter with 17%

+-----------------------+-------+--------+
| Key                   | Value | Factor |
+-----------------------+-------+--------+
| size                  | 84810 | 0.2    |
| stargazersCount       | 11369 | 0.7    |
| forks                 | 5847  | 1      |
| openIssues            | 74    | 0.2    |
| subscribersCount      | 1520  | 1      |
| userPublicRepos       | 5     | 0.2    |
| commitsCount          | 713   | 0.5    |
| commitsLastMonthCount | 10    | 0.8    |
| avgCommitsPerWeek     | 13    | 2      |
| contributorsCount     | 467   | 1      |
+-----------------------+-------+--------+

4. cakephp/cakephp with 15%

+-----------------------+-------+--------+
| Key                   | Value | Factor |
+-----------------------+-------+--------+
| size                  | 98871 | 0.2    |
| stargazersCount       | 6214  | 0.7    |
| forks                 | 2848  | 1      |
| openIssues            | 165   | 0.2    |
| subscribersCount      | 627   | 1      |
| userPublicRepos       | 47    | 0.2    |
| commitsCount          | 2080  | 0.5    |
| commitsLastMonthCount | 107   | 0.8    |
| avgCommitsPerWeek     | 40    | 2      |
| contributorsCount     | 498   | 1      |
+-----------------------+-------+--------+

5. laravel/laravel with 15%

+-----------------------+-------+--------+
| Key                   | Value | Factor |
+-----------------------+-------+--------+
| size                  | 22086 | 0.2    |
| stargazersCount       | 20831 | 0.7    |
| forks                 | 6747  | 1      |
| openIssues            | 29    | 0.2    |
| subscribersCount      | 2823  | 1      |
| userPublicRepos       | 20    | 0.2    |
| commitsCount          | 263   | 0.5    |
| commitsLastMonthCount | 15    | 0.8    |
| avgCommitsPerWeek     | 5     | 2      |
| contributorsCount     | 370   | 1      |
+-----------------------+-------+--------+

6. yiisoft/yii2 with 12%

+-----------------------+-------+--------+
| Key                   | Value | Factor |
+-----------------------+-------+--------+
| size                  | 52541 | 0.2    |
| stargazersCount       | 7255  | 0.7    |
| forks                 | 4293  | 1      |
| openIssues            | 1050  | 0.2    |
| subscribersCount      | 1125  | 1      |
| userPublicRepos       | 29    | 0.2    |
| commitsCount          | 1884  | 0.5    |
| commitsLastMonthCount | 78    | 0.8    |
| avgCommitsPerWeek     | 36    | 2      |
| contributorsCount     | 655   | 1      |
+-----------------------+-------+--------+

The number of requests remaining in the current rate limit window: 4983.
```

## TODO
* output remaining requests count;
* check if github repository exists before attempting to build a Repository object;
* add factor name in the annotation definition;
* make factor values configurable;
* add progress bar during building;
* add docker support;
* travis;
* README.md.