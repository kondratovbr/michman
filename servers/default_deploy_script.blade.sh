#!/bin/bash

cd /home/{{ $project->serverUsername }}/{{ $project->domain }} || exit

git pull origin "$MICHMAN_BRANCH"

source venv/bin/activate

pip install -r requirements.txt

./manage.py migrate --noinput

./manage.py collectstatic --noinput
