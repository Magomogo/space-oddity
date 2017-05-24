backend.dependencies:
  cmd.run:
    - name: composer install --no-interaction
    - cwd: /vagrant/backend
    - runas: {{ pillar.user }}
    - unless: composer install --dry-run --no-interaction 2>&1 | grep -q 'Nothing to install or update'
    - require:
      - pkg: corepkgs

apache2_rewrite:
  cmd.run:
    - name: a2enmod rewrite ; echo -e "\nChanged=yes\n"
    - stateful: True
    - unless: a2enmod -q rewrite
    - require_in:
      - service: apache2

/etc/apache2/sites-available/acmepay.conf:
  file.managed:
    - source: salt://backend/vhost.conf
    - template: jinja
    - user: root
    - group: root
    - mode: 644
    - watch_in:
      - service: apache2

/etc/apache2/sites-enabled/acmepay.conf:
  file.symlink:
    - target: /etc/apache2/sites-available/acmepay.conf
    - require_in:
      - service: apache2

/etc/apache2/sites-enabled/000-default.conf:
  file.absent:
    - require_in:
      - service: apache2

include:
  - .database
