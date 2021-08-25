# The number of worker processes to run.
# Should generally be 2-4 times the server's core count,
# if the server isn't used for anything else but running this project.
workers = 3

# A worker process will be reloaded after handling this many requests.
# Provides basic protection from memory leaks.
max_requests = 100
