import Sequelize from 'sequelize';
import getConfig from '../config/configES.js';
import { NODE_ENV } from '$env/static/private';

// Import all models
import setupUser from './User.js';

const db = {};

export function dbInit() {
    db.Sequelize = Sequelize;
    const config = getConfig(NODE_ENV);

    db.sequelize = new Sequelize(
        config.database,
        config.username,
        config.password,
        config
    );

    // Call model setup functions
    db.User = setupUser(db.sequelize, Sequelize.DataTypes);

    // Call model associate functions
    db.User.associate(db);
}

// Export all models
export default db;
