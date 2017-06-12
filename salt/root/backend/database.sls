trust-soket-connections:
  file.replace:
    - name: /etc/postgresql/9.4/main/pg_hba.conf
    - pattern: local\s+all\s+postgres\s+peer
    - repl: local all postgres trust
    - count: 1
    - require:
      - pkg: postgresql
    - watch_in:
      - service: postgresql

connections-from-host-allowed:
  file.append:
    - name: /etc/postgresql/9.4/main/pg_hba.conf
    - text: host all all 192.168.55.1/32 trust
    - require:
      - pkg: postgresql
    - watch_in:
      - service: postgresql

/etc/postgresql/9.4/main/postgresql.conf:
  file.append:
    - text: listen_addresses = '*'
    - require:
      - pkg: postgresql
    - watch_in:
      - service: postgresql

acmepay:
  postgres_user.present:
    - password: acmepay
    - superuser: true
    - require:
      - service: postgresql

  postgres_database.present:
    - require:
      - service: postgresql

database-schema-is-deployed:
  cmd.wait_script:
    - source: salt://backend/database_schema_install.sh
    - watch:
      - postgres_database: acmepay

database-is-filled-in:
  cmd.wait:
    - name: /vagrant/cli/do-fake-transactions.php
    - runas: {{ pillar.user }}
    - watch:
      - cmd: database-schema-is-deployed
