import { startStimulusApp } from '@symfony/stimulus-bridge';
import ApplicationTaskEditController from "./controllers/application_task_edit_controller";

export const app = startStimulusApp(require.context(
    '@symfony/stimulus-bridge/lazy-controller-loader!./controllers',
    true,
    /\.[jt]sx?$/
));

app.register('application-task-crud-edit', ApplicationTaskEditController);