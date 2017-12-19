FROM mysql:5.6
MAINTAINER pushaowei

COPY ./install/*   /tmp/install/

RUN ln -s /tmp/install/entrypoint.sh /entrypoint.sh
RUN pwd
RUN ls -l

ENTRYPOINT ["sh","entrypoint.sh"]

EXPOSE 3306
CMD ["mysqld"]
