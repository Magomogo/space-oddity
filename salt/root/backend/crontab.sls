/vagrant/backend/cli/invalidate-cache.php:
  cron.present:
    - identifier: invalidate-cache-daily
    - user: {{ pillar.user }}
    - minute: 0
    - hour: 0
    - require:
      - pkg: corepkgs
