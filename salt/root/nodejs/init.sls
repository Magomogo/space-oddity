/root/nodesource.gpg.key:
  file.managed:
    - source: salt://nodejs/nodesource.gpg.key
    - mode: 644

apt-add-nodesource-key:
  cmd.wait:
    - name: apt-key add /root/nodesource.gpg.key && /bin/echo -e '\nchanged=yes\n'
    - cwd: /root
    - stateful: True
    - unless: apt-key --list | grep ^uid *NodeSource <gpg@nodesource.com>'
    - watch:
      - file: /root/nodesource.gpg.key

/etc/apt/sources.list.d/nodesource.list:
  file.managed:
    - contents:
        deb https://deb.nodesource.com/node_6.x jessie main

apt-update-nodesource-repo:
  cmd.wait:
    - name: apt-get update; /bin/echo -e '\nchanged=yes\n'
    - stateful: True
    - watch:
      - file: /etc/apt/sources.list.d/nodesource.list
      - cmd: apt-add-nodesource-key

node:
  pkg.installed:
    - name: nodejs
    - require:
      - cmd: apt-update-nodesource-repo
