# Generating IDs

Unique IDs are really useful. For example, when doing an install session, you might want a unique ID to track that session. (Although for installs one could argue that you should only be installing once at a time.)

When doing things such as UUIDs, you can use the standard UUID function:
https://www.php.net/manual/en/function.uniqid.php

And make it smaller using `base_convert()`:
https://stackoverflow.com/a/308306/1086584
