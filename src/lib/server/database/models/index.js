import Sequelize from 'sequelize';

// Import all models
// ...

import { NODE_ENV } from '$env/static/private';

const db = {};

export function init() {
    db.Sequelize = Sequelize;

    db.sequelize = new Sequelize(
        configAll(NODE_ENV).database, 
        configAll(NODE_ENV).username, 
        configAll(NODE_ENV).password,
        configAll(NODE_ENV)
    );

    // Call model setup functions
    // ...

    // Call model associate functions
    // ...
}

// Export all models
export default db;
