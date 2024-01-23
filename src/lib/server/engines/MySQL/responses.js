export class ResponseOk {
    constructor() {
        this.type = 'OK';
        this.fields = [];
        this.records = [];
    }
}

export class ResponseRecords {
    constructor(records, fields) {
        this.type = 'RECORDS';
        this.records = records;
        this.fields = fields.map(field => field.name);
    }
}

export class ResponseError {
    constructor(error) {
        this.type = 'ERROR';
        this.message = error.message;
        this.fields = [];
        this.records = [];
    }
}