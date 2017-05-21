/home/{{ pillar.user }}/.bashrc:
  file.uncomment:
    - regex: alias ll

/home/{{ pillar.user }}/.gitconfig:
  file.managed:
    - source: salt://environment/.gitconfig
    - template: jinja
    - user: {{ pillar.user }}
    - group: {{ pillar.group_www }}
    - mode: 600

{{ pillar.user }}:
  user.present:
    - gid: {{ pillar['group_www'] }}
    - remove_groups: False
    - require:
      - pkg: corepkgs
