import bcrypt from 'bcrypt';
import { Model } from 'sequelize';
import generateToken from '$lib/helpers/generateToken.js';

import { env } from '$env/dynamic/private';

export default (sequelize, DataTypes) => {
	class User extends Model {
		/**
		 * Helper method for defining associations.
		 * This method is not a part of Sequelize lifecycle.
		 * The `models/index` file will call this method automatically.
		 */
		static associate(models) {
			// ...
		}
	}
	User.init({
		hash: DataTypes.STRING(32),
		query: {
			type: DataTypes.TEXT,

			get() {
				return JSON.parse(this.getDataValue('query'));
			},

			set(value) {
				this.setDataValue('query', JSON.stringify(value));
			}
		},
	}, {
		sequelize,
		modelName: 'Query',
		tableName: 'queries',
		timestamps: true,
	});
	return User;
};