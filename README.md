# SimpleEloquent ORM

[![Build Status](https://img.shields.io/travis/PuShaoWei/SimpleEloquent.svg?style=flat-square)](https://travis-ci.org/PuShaoWei/SimpleEloquent)
[![Coverage Status](https://img.shields.io/codecov/c/github/PuShaoWei/SimpleEloquent.svg?style=flat-square)](https://codecov.io/github/PuShaoWei/SimpleEloquent)
[![Scrutinizer](https://img.shields.io/scrutinizer/g/PuShaoWei/SimpleEloquent.svg?style=flat-square)](https://scrutinizer-ci.com/g/PuShaoWei/SimpleEloquent/?branch=master)
[![Latest Stable Version](https://img.shields.io/packagist/v/marco/simple-eloquent.svg?style=flat-square&label=stable)](https://packagist.org/packages/marco/SimpleEloquent)
[![License](https://img.shields.io/packagist/l/marco/simple-eloquent.svg?style=flat-square)](https://packagist.org/packages/marco/SimpleEloquent)

## Installation

Use the following command to install

```bash
composer require marco/simple-eloquent 
```

## Application step

> Really don't have too much time delay in the docker mysql on the mirror, so I gave in, manual, please...

Running with docker-compos

Step1: Start the container

```
docker-compose up --build -d
```

Step2: Connected to the master, and run the following command to create a user used to synchronize data

```
GRANT REPLICATION SLAVE ON *.* to 'backup'@'%' identified by '123456';
```

Step3: See master status, remember the File, the value of the Position, if you don't have to check the data, please check the first and the second step, the configuration problems. I found out that  master-bin.000017, 312

```
show master status;
```

Step4: Connect the slave, run the following command to connect the master

```
change master to master_host='smart_master',master_user='backup',master_password='123456', master_log_file='master-bin.000004',master_log_pos=320;
```

Step5: Start the slave
```
start slave;
```

Step6: Check the slave status.
```
show slave status\G
```
If you see  `Waiting for master send event` indicates success, you are now in the main library on the modification, synchronization to from the library.

## About

This is a simplified version of the Eloquent

## Correct the mistakes

If you find something wrong, you can initiate a [issue](https://github.com/PuShaoWei/dhildish/issues)or [pull request](https://github.com/PuShaoWei/dhildish/pulls),I will correct it in time

## Contributors

<table>
    <tbody>
        <tr>
            <td ><a href="https://github.com/PuShaoWei"><img src="https://avatars2.githubusercontent.com/u/18391791?v=1" /></a>
            <p align="center">Marco</p>
            </td>
        </tr>
    </tbody>
</table>

## License

MIT


 
