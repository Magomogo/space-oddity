backend.dependencies:
  cmd.run:
    - name: composer install --no-interaction
    - cwd: /vagrant/backend
    - runas: {{ pillar.user }}
    - unless: composer install --dry-run --no-interaction 2>&1 | grep -q 'Nothing to install or update'
    - require:
      - pkg: corepkgs
