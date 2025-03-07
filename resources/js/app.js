import { createApp } from 'vue'; // Используем Vue 3
import ExampleComponent from './components/ExampleComponent.vue'; // Импортируем компонент

const app = createApp(ExampleComponent); // Регистрируем компонент
app.mount('#app'); // Монтируем компонент в элемент с id="app"