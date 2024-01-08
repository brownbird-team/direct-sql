const rand = () => {
    return Math.random().toString(36).split('.')[1];
}

export default function generateToken(len) {
    if (!len)
        return rand() + rand() + rand() + rand();

    let token = '';

    for (let i = 0; i < Math.floor(len / 11); i++)
        token += rand();

    token += rand().slice(0, len % 11);
    return token;
}