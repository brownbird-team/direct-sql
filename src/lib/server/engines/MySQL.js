import mysql from 'mysql';
import * as errors from './errors.js'

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
        return new Promise((resolve, reject) => {
            this.conn.query('SHOW DATABASES', (err, results, fields) => {
                console.log("Results => ", err, results, fields);
            });
        });
    }



    
}

export default MySQL