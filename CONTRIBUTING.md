# Contributing to VINE

We follow a simplified [Gitflow] methodology. The _main_ branch reflects
production code and is automatically deployed via Github Actions. We never
push commits directly to _main_ and don't point pull requests there unless
it's a very urgent hotfix.

The _develop_ branch should be the target for all your pull requests. The
code is automatically deployed to the staging environment for testing.

## Creating a pull request

Name your branch `feature/...` to describe the thing you are developing.
For example, the branch `feature/multi-merchant` added the functionality
to connect multiple merchants to one voucher set.

In rare cases, we may need to bypass the _develop_ branch and fix the _main_
branch. Then name your branch `hotfix/...` and include only the minimal
changes necessary to fix the bug.

In any case, commit frequently to your branch to create a Git history that's
as easy to read like a book, page by page. Commit messages should explain the
_why_ of your code change. The _what_ can usually be observed in the diff.

Before you publish your pull request for review, make sure it's based on the
latest commit of the _develop_ branch for features or on the latest commit of
the _main_ branch for hotfixes. Use `git rebase` before publishing your code.

## The release process

New pull requests will be reviewed and merged by one of our team members.
Manual testing is done on the staging server before we merge to _main_ and
release new code to production.

When merging to _main_ we decide about the version number of the next release.
We follow [semantic versioning]. Patches receive a new version without release
notes. And new features are marked as minor change with release notes.

[Gitflow]: https://www.atlassian.com/git/tutorials/comparing-workflows/gitflow-workflow
[semantic versioning]: https://semver.org/
