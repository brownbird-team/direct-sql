import mysql from 'mysql';
import * as errors from './errors.js';
import generateToken from '../../../lib/helpers/generateToken.js';
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
                if (this.err) {
                    reject(err);
                    return;
                }

                resolve();
            });
        });
    }

    execute(query) {
        return new Promise((resolve, reject) => {
            this.conn.query(query, (err, results, fields) => {
                const result = {
                    error: null,
                    results: [],
                    fields: [],
                }

                if (err) {
                    result.error = err.sqlMessage;
                    resolve(result);
                    return;
                }

                if (!Array.isArray(results[0])) {
                    fields = [ fields ];
                    results = [ results ];
                }

                results.forEach((res, index) => {
                    fld = fields[index];

                    result.results.push({
                        
                    });

                });

                console.log("QUERY => ", err, results, fields);
                
                resolve(result);
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

                resolve();
            });
        })
    }



    
}

export default MySQL