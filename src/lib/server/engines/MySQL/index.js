import mysql from 'mysql2/promise';
import * as errors from '../errors.js';
import generateToken from '../../../helpers/generateToken.js';

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
 * 
 */


class MySQL {
    constructor(config) {
        this.config = config;
    }

    async connect() {
        this.conn = await mysql.createConnection({
            host: this.config.host,
            port: this.config.port,
            user: this.config.username,
            password: this.config.password,
            multipleStatements: true,
            rowsAsArray: true,
        });
    }

    async setup() {
        this.id = generateToken(12);
        this.database = `pandasql_tempdb_${this.id}`;
        this.username = `pandasql_tempuser_${this.id}`;
        this.password = generateToken(64);

        try {
            await this.conn.query(`
                CREATE DATABASE ${this.database};
                CREATE USER '${this.username}'@'%' IDENTIFIED BY '${this.password}';
                GRANT ALL PRIVILEGES ON ${this.database}.* TO '${this.username}'@'%';
                FLUSH PRIVILEGES;
            `);
        } catch (err) {
            throw new errors.EngineSystemError(err.sqlMessage, err.code);
        }

        this.tempConn = await mysql.createConnection({
            host: this.config.host,
            port: this.config.port,
            user: this.username,
            password: this.password,
            database: this.database,
            multipleStatements: true,
            rowsAsArray: true,
        });
    }

    async execute(query) {

        try {
            const [results, fields] = await this.tempConn.query(query);
            return this.parseQueryResults(results, fields);

        } catch (err) {
            return [{
                type: 'ERROR',
                fields: [],
                results: [],
                error: {
                    code: err.code,
                    message: err.sqlMessage,
                },
            }];
        }
    }

    async end() {
        try {
            await this.conn.query(`
                REVOKE ALL PRIVILEGES ON ${this.database}.* FROM '${this.username}'@'%';
                DROP DATABASE ${this.database};
                DROP USER '${this.username}'@'%';
            `);

            this.conn.destroy();

        } catch (err) {
            throw new errors.EngineSystemError(err.sqlMessage, err.code);
        }
    }

    parseQueryResults(results, fields) {

        if (this.isRecordSet(results)) {
            return [
                this.prepareRecordsSet(results, fields)
            ];
        }

        if (this.isResultSetHeader(results)) {
            return [
                this.prepareResultSetHeader(results, fields)
            ]
        }

        return results.map((res, index) => {
            if (this.isRecordSet(res))
                return this.prepareRecordsSet(res, fields[index]);

            if (this.isResultSetHeader(res))
                return this.prepareResultSetHeader(res, fields[index]);
        });
    }

    isRecordSet(variable) {
        return Array.isArray(variable) && !variable.find(
            v => !Array.isArray(v) || typeof(v[0]) == 'object'
        );
    }

    prepareRecordsSet(results, fields) {
        return {
            type: 'RECORDS',
            fields: fields.map(field => field.name),
            results: results,
        }
    }

    isResultSetHeader(variable) {
        return typeof(variable) == 'object' 
            && typeof(variable?.serverStatus) == 'number';
    }

    prepareResultSetHeader(results, fields) {
        return {
            type: 'OPERATION',
            fields: [],
            results: [],
        }
    }
}

export default MySQL