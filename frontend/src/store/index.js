import { configureStore } from '@reduxjs/toolkit';
import todosReducer from '../features/todos/todosSlice';
import categoriesReducer from '../features/categories/categoriesSlice';
import themeReducer from '../features/theme/themeSlice';

export const store = configureStore({
  reducer: {
    todos: todosReducer,
    categories: categoriesReducer,
    theme: themeReducer,
  },
});
