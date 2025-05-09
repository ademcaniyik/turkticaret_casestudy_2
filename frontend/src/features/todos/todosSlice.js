import { createSlice, createAsyncThunk } from '@reduxjs/toolkit';
import axios from 'axios';

const API_URL = process.env.REACT_APP_API_URL || 'http://localhost:8000/api';

export const fetchTodos = createAsyncThunk(
  'todos/fetchTodos',
  async () => {
    const response = await axios.get(`${API_URL}/todos`);
    return response.data;
  }
);

export const createTodo = createAsyncThunk(
  'todos/createTodo',
  async (todoData) => {
    const response = await axios.post(`${API_URL}/todos`, todoData);
    return response.data;
  }
);

export const updateTodo = createAsyncThunk(
  'todos/updateTodo',
  async (todoData) => {
    const response = await axios.put(`${API_URL}/todos/${todoData.id}`, todoData);
    return response.data;
  }
);

export const deleteTodo = createAsyncThunk(
  'todos/deleteTodo',
  async (id) => {
    await axios.delete(`${API_URL}/todos/${id}`);
    return id;
  }
);

const todosSlice = createSlice({
  name: 'todos',
  initialState: {
    items: [],
    status: 'idle',
    error: null,
  },
  reducers: {
    setTodos: (state, action) => {
      state.items = action.payload;
    },
  },
  extraReducers: (builder) => {
    builder
      .addCase(fetchTodos.pending, (state) => {
        state.status = 'loading';
      })
      .addCase(fetchTodos.fulfilled, (state, action) => {
        state.status = 'succeeded';
        state.items = action.payload;
      })
      .addCase(fetchTodos.rejected, (state, action) => {
        state.status = 'failed';
        state.error = action.error.message;
      })
      .addCase(createTodo.fulfilled, (state, action) => {
        state.items.push(action.payload);
      })
      .addCase(updateTodo.fulfilled, (state, action) => {
        const index = state.items.findIndex(todo => todo.id === action.payload.id);
        if (index !== -1) {
          state.items[index] = action.payload;
        }
      })
      .addCase(deleteTodo.fulfilled, (state, action) => {
        state.items = state.items.filter(todo => todo.id !== action.payload);
      });
  },
});

export const { setTodos } = todosSlice.actions;
export default todosSlice.reducer;
