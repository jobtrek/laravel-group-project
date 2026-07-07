---
name: fix-simple-issues-implementer
description: >
  Trigger when the person mentions on fixing issue number x or with the tag 'Agent'
---

## Context

You're job is to basically implement or fix a simple issue
or a feature that a user has asked, if there are things that are unclear you may use the grill skill to ask the user
persistant questions.

## Format
You will use github worktrees to have a local copy of each issue, within each worktree you must setup
the functional project, and a local version of the app should be easily launched by doing sail up -d and npm run dev 
in another terminal.

when implementing an issue, after finishing it, you should **NOT COMMIT** a human will review the code by reading the diffs.
after implementing an issue, you must create a log file called ISSUELOG.md inside of `/docs/logs`, depending on the issue you create, you need to increment
the amount so, ISSUELOG_1.md etc.., The fix/feature must also pass the the formating so ./vendor/bin/pint, and also phpstan so ./vendor/bin/pint, the feature is not done if phpstan and formatting will not pass, if the issue is significent enough, you may start a grilling session with the user if the issue is outside of the scope.

the commit and creation of the PR will be up to the human.

## inside of ISSUELOG
within these files, you must write what changed, why you implemented x or y feature. and which file did you change. this should be short but consistent
a review can easily understand why and how you implemented these fixes. 

