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

acmepay:
  postgres_user.present:
    - password: acmepay
    - require:
      - service: postgresql

  postgres_database.present:
    - require:
      - postgres_user: acmepay
      - service: postgresql

database-schema-is-deployed:
  cmd.wait_script:
    - source: salt://backend/database_schema_install.sh
    - watch:
      - postgres_database: acmepay
