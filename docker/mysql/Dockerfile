FROM mariadb:10.1

ADD my.cnf /etc/mysql/my.cnf
RUN chmod 0444 /etc/mysql/my.cnf
RUN cp /usr/share/zoneinfo/Europe/Warsaw /etc/localtime