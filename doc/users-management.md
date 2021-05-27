# Users Management

Fetching a list with all users and their corresponding id:

```shell
bin/console user:list
```

Create a new user and replace `[NAME]` with the desired name of the user. Set `[ROLE]` that's either `ROLE_USER` or `ROLE_ADMIN`. The `[BRANCH]` is the id of the branch the user is supposed to be a part of.

```shell
bin/console user:new [NAME] [ROLE] [BRANCH]
```

You can of course delete a user. Replace `[ID]` with the id of the user.

```shell
bin/console user:delete [ID]
```

If the user has forgotten the password, you can reset it with this command. Replace `[ID]` with the id of the user.

```shell
bin/console user:reset-password [ID]
```
