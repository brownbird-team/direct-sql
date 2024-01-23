import mysql from 'mysql';
import * as errors from '../errors.js';
import generateToken from '../../../helpers/generateToken.js';
import { ResponseRecords, ResponseOk, ResponseError,  } from './responses.js';
//import { env } from '$env/dynamic/private';

/*
 * MySQL database engine class
 *
 * This class is used to create one time database on specified database
 * host, execute given list of queries and then delete created database
 * 
 * In the future it will probably implement some other logic too
 * 
 * config object props
 * 
 * => host
 * => port
 * => username
 * => password
 * => dbprefix (default "pandasql_temdb_")
 * 
 */


class MySQL {
    constructor(config) {
        this.conn = mysql.createConnection({
            host: config.host,
            port: config.port,
            user: config.username,
            password: config.password,
            multipleStatements: true,
        });
    }

    connect() {
        return new Promise((resolve, reject) => {
            this.conn.connect((err) => {
                if (err) {
                    reject(err);
                    return;
                }

                resolve(this);
            });
        });
    }

    setup() {
        this.id = generateToken(12);
        this.database = `pandasql_tempdb_${this.id}`;
        this.username = `pandasql_tempuser_${this.id}`;
        this.password = generateToken(64);

        return new Promise((resolve, reject) => {
            this.conn.query(`
                CREATE DATABASE ${this.database};
                CREATE USER '${this.username}'@'%' IDENTIFIED BY '${this.password}';
                GRANT ALL PRIVILEGES ON ${this.database}.* TO '${this.username}'@'%';
                FLUSH PRIVILEGES;
                USE ${this.database};
            `, (err) => {
                if (err) {
                    reject(err);
                    return;
                }

                resolve();
            });
        });
    }

    /**
     * AHDSSADSA
     * @param {*} query sdajdsjdhad
     * @returns sdadwdadsa
     */
    execute(query) {
        return new Promise((resolve, reject) => {
            this.conn.query(query, (err, results, fields) => {

                if (err) {
                    reject(ResponseError(err));
                    return;
                }

                resolve(this.parseQueryResults(results, fields));
            });
        });
    }

    end() {
        return new Promise((resolve, reject) => {
            this.conn.query(`
                REVOKE ALL PRIVILEGES ON ${this.database}.* FROM '${this.username}'@'%';
                DROP DATABASE ${this.database};
                DROP USER '${this.username}'@'%';
            `, (err) => {
                this.conn.end();

                if (err) {
                    reject(err);
                    return;
                }

                resolve();results
            });
        })
    }

    parseQueryResults(results, fields) {
        const res = {
            fields: [],
            results: [],
        };

        if (!results)
            return res;

        const structure = (results, fields) => {
            if (results instanceof Array) {
                if (results[0] instanceof mysql.RowDataPacket) {
                    return [ new ResponseRecords(results, fields) ];
                }

                const queryResults = [];

                results.forEach((queryPart, index) => {
                    if (queryPart instanceof mysql.OkPacket) {
                        queryResults.push(new ResponseOk());
                    } 
                    else if (queryPart instanceof mysql.RowDataPacket) {
                        queryResults.push(new ResponseRecords([ queryPart ], [ fields[index] ]))
                    }
                    else {
                        queryResults.push(new ResponseRecords(queryPart, fields[index]));
                    }
                });

                return queryResults;

            } else {
                if (results instanceof mysql.OkPacket) {
                    return [ new ResponseOk() ]
                }
                if (results instanceof mysql.RowDataPacket) {
                    return [ new ResponseRecords([ results ]) ];
                }
            }
        }
    }
}

export default MySQL