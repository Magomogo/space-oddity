frontend.dependencies:
  npm.bootstrap:
    - name: /vagrant/frontend
    - user: {{ pillar.user }}
    - require:
      - pkg: node

frontend.built:
  cmd.wait:
    - name: npm run build
    - cwd: /vagrant/frontend
    - runas: {{ pillar.user }}
    - watch:
      - npm: frontend.dependencies
