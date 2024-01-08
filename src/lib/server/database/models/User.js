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

		// Find user instance with given token
		static async findByToken(token, options = {}) {
			const user = await User.findOne({
				...options,
				where: { token },
			});

			return (user && (await user.validateToken(token))) ? user : null;
		}
		
		// Check if password is valid
		async authenticate(pass) {
			return await bcrypt.compare(pass, this.password);
		}
		// Check if given token is valid token
		async validateToken(token) {
			return this.token && this.token === token && this.tokenExpires > (new Date());
		}
        // Generate token and set expire time
        async generateToken(remember) {
            this.token = generateToken();
            this.tokenExpires = new Date(
				Date.now() + 
				Number(remember ? env.AUTH_TOKEN_REMEMBER_VALID_SEC : env.AUTH_TOKEN_VALID_SEC) * 1000
			);

			await this.save();
            return this.tokenExpires;
        }
	}
	User.init({
		username: DataTypes.STRING(1023),

		password: {
			type: DataTypes.STRING(1023),

			set(value) {
				this.setDataValue('password', bcrypt.hashSync(
                    value,
                    (Number(env.PASSWORD_SALT_ROUNDS) || 10)
                ));
			}
		},
		
		token: DataTypes.STRING(1023),
		tokenExpires: DataTypes.DATE,
	}, {
		sequelize,
		modelName: 'User',
		tableName: 'users',
		timestamps: false,
	});
	return User;
};