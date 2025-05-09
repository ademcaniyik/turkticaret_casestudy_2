import { createSlice, createAsyncThunk } from '@reduxjs/toolkit';
import axios from 'axios';

const API_URL = process.env.REACT_APP_API_URL || 'http://localhost:8000/api';

export const fetchCategories = createAsyncThunk(
  'categories/fetchCategories',
  async () => {
    const response = await axios.get(`${API_URL}/categories`);
    return response.data;
  }
);

export const createCategory = createAsyncThunk(
  'categories/createCategory',
  async (categoryData) => {
    const response = await axios.post(`${API_URL}/categories`, categoryData);
    return response.data;
  }
);

export const updateCategory = createAsyncThunk(
  'categories/updateCategory',
  async (categoryData) => {
    const response = await axios.put(`${API_URL}/categories/${categoryData.id}`, categoryData);
    return response.data;
  }
);

export const deleteCategory = createAsyncThunk(
  'categories/deleteCategory',
  async (id) => {
    await axios.delete(`${API_URL}/categories/${id}`);
    return id;
  }
);

const categoriesSlice = createSlice({
  name: 'categories',
  initialState: {
    items: [],
    status: 'idle',
    error: null,
  },
  reducers: {
    setCategories: (state, action) => {
      state.items = action.payload;
    },
  },
  extraReducers: (builder) => {
    builder
      .addCase(fetchCategories.pending, (state) => {
        state.status = 'loading';
      })
      .addCase(fetchCategories.fulfilled, (state, action) => {
        state.status = 'succeeded';
        state.items = action.payload;
      })
      .addCase(fetchCategories.rejected, (state, action) => {
        state.status = 'failed';
        state.error = action.error.message;
      })
      .addCase(createCategory.fulfilled, (state, action) => {
        state.items.push(action.payload);
      })
      .addCase(updateCategory.fulfilled, (state, action) => {
        const index = state.items.findIndex(category => category.id === action.payload.id);
        if (index !== -1) {
          state.items[index] = action.payload;
        }
      })
      .addCase(deleteCategory.fulfilled, (state, action) => {
        state.items = state.items.filter(category => category.id !== action.payload);
      });
  },
});

export const { setCategories } = categoriesSlice.actions;
export default categoriesSlice.reducer;
