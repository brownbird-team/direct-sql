import { env } from '$env/dynamic/private';

const getConfig = (environment) => ({
    
    development: {
        dialect: 'mysql',
  
        host: env.DEV_DB_HOST,
        port: env.DEV_DB_PORT,
        database: env.DEV_DB_NAME,
        username: env.DEV_DB_USERNAME,
        password: env.DEV_DB_PASSWORD,
  
        migrationStorage: 'sequelize',
        migrationStorageTableName: 'migrations',
        seederStorage: 'sequelize',
        seederStorageTableName: 'seeds',
    },
  
    production: {
        dialect: 'mysql',
        
        host: env.PROD_DB_HOST,
        port: env.PROD_DB_PORT,
        database: env.PROD_DB_NAME,
        username: env.PROD_DB_USERNAME,
        password: env.PROD_DB_PASSWORD,
  
        migrationStorage: 'sequelize',
        migrationStorageTableName: 'migrations',
        seederStorage: 'sequelize',
        seederStorageTableName: 'seeds',
    },

}[environment]);

export default getConfig;