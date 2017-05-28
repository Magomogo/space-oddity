corepkgs:
  pkg.installed:
    - pkgs:
      - php5
      - php5-pgsql
      - curl
      - git

composer.installed:
  file.managed:
    - source: https://getcomposer.org/download/1.4.2/composer.phar
    - name: /usr/local/bin/composer
    - source_hash: 4400106431775fc9fc45acfb875b6e34ab3fea62
    - mode: 0775
    - unless: composer --version
    - require:
      - pkg: corepkgs

apache2:
  pkg:
    - installed
  service:
    - running
    - enable: True

postgresql:
  pkg:
    - installed
  service:
    - running
    - enable: True

acmepay.local:
  host.present:
    - ip: 127.0.0.1
