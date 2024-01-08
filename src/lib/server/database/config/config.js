module.exports = {
        
    development: {
            dialect: 'mysql',

            host: process.env.DEV_DB_HOST,
            port: process.env.DEV_DB_PORT,
            database: process.env.DEV_DB_NAME,
            username: process.env.DEV_DB_USERNAME,
            password: process.env.DEV_DB_PASSWORD,

            migrationStorage: 'sequelize',
            migrationStorageTableName: 'migrations',
            seederStorage: 'sequelize',
            seederStorageTableName: 'seeds',
    },

    production: {
            dialect: 'mysql',
            
            host: process.env.PROD_DB_HOST,
            port: process.env.PROD_DB_PORT,
            database: process.env.PROD_DB_NAME,
            username: process.env.PROD_DB_USERNAME,
            password: process.env.PROD_DB_PASSWORD,

            migrationStorage: 'sequelize',
            migrationStorageTableName: 'migrations',
            seederStorage: 'sequelize',
            seederStorageTableName: 'seeds',
    },
}