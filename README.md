# PandaSQL

PandaSQL is platform used to practice and learn MySQL. Development of PandaSQL is still in progress, but we should finish it by the end of this school year. More details about this project will be present in documentation once we write it.

**Option 1** 
Platform itself will be written in JS and will use NodeJS as backend. For HTTP API we plan to use Express JS library. About MySQL interface, we are still considering multiple options, but we might end up choosing even simple mysql library. Frontend will be written using React or Svelte JS frameworks.

**Option 2 (currently preferred)** 
Platform will still be written in JS with NodeJS, but now we are considering using SvelteKit framework. When it comes to database interaction, we will use Sequelize for interactions with storage database and plain mysql library for an engine class. Since we are using SvelteKit it is clear that Svelte is our choice for frontend.

Also whole project and it's documentation will from now on be written in English.

`TODO: Write proper readme`

## Developing

Once you've created a project and installed dependencies with `npm install` (or `pnpm install` or `yarn`), start a development server:

```bash
npm run dev

# or start the server and open the app in a new browser tab
npm run dev -- --open
```

## Building

To create a production version of your app:

```bash
npm run build
```

You can preview the production build with `npm run preview`.

> To deploy your app, you may need to install an [adapter](https://kit.svelte.dev/docs/adapters) for your target environment.
