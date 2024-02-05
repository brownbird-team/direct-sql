export class EngineUserError extends Error {
    constructor(message, code = 'ERROR_UNDEFINED') {
        super(message);
        this.code = code;
    }
}

export class EngineSystemError extends Error {
    constructor(message, code = 'ERROR_UNDEFINED') {
        super(message);
        this.code = code;
    }
}