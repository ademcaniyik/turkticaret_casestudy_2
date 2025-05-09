import React, { useState } from 'react';
import { useSelector, useDispatch } from 'react-redux';
import { useParams, useNavigate } from 'react-router-dom';
import { Box, Paper, Typography, TextField, Button, FormControl, InputLabel, Select, MenuItem, Chip, IconButton, Dialog, DialogTitle, DialogContent, DialogActions } from '@mui/material';
import { Edit, Delete, Save, Cancel } from '@mui/icons-material';
import { deleteTodo, updateTodo } from '../features/todos/todosSlice';

const TodoDetail = () => {
  const { id } = useParams();
  const dispatch = useDispatch();
  const navigate = useNavigate();
  const todos = useSelector((state) => state.todos.items);
  const todo = todos.find(t => t.id === parseInt(id));

  const [editing, setEditing] = useState(false);
  const [formData, setFormData] = useState({
    title: todo?.title || '',
    description: todo?.description || '',
    status: todo?.status || 'pending',
    priority: todo?.priority || 'medium',
    due_date: todo?.due_date || '',
  });

  const [deleteDialogOpen, setDeleteDialogOpen] = useState(false);

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: value,
    }));
  };

  const handleSave = () => {
    if (todo) {
      dispatch(updateTodo({
        id: todo.id,
        ...formData,
      }));
      setEditing(false);
    }
  };

  const handleDelete = () => {
    if (todo) {
      dispatch(deleteTodo(todo.id));
      navigate('/todos');
    }
  };

  const handleCancel = () => {
    setFormData({
      title: todo?.title || '',
      description: todo?.description || '',
      status: todo?.status || 'pending',
      priority: todo?.priority || 'medium',
      due_date: todo?.due_date || '',
    });
    setEditing(false);
  };

  if (!todo) {
    return (
      <Box sx={{ p: 3 }}>
        <Typography variant="h6">Todo Bulunamadı</Typography>
      </Box>
    );
  }

  return (
    <Box sx={{ p: 3 }}>
      <Paper sx={{ p: 3 }}>
        <Typography variant="h5" gutterBottom>
          {editing ? (
            <TextField
              fullWidth
              name="title"
              value={formData.title}
              onChange={handleInputChange}
              margin="none"
            />
          ) : (
            todo.title
          )}
        </Typography>

        <Box sx={{ mb: 2 }}>
          <Typography variant="subtitle1">Açıklama</Typography>
          {editing ? (
            <TextField
              fullWidth
              multiline
              rows={4}
              name="description"
              value={formData.description}
              onChange={handleInputChange}
              margin="none"
            />
          ) : (
            <Typography>{todo.description}</Typography>
          )}
        </Box>

        <Box sx={{ display: 'flex', gap: 2, mb: 2 }}>
          <Typography variant="subtitle1">Durum</Typography>
          {editing ? (
            <FormControl>
              <InputLabel>Status</InputLabel>
              <Select
                name="status"
                value={formData.status}
                onChange={handleInputChange}
              >
                <MenuItem value="pending">Bekleyen</MenuItem>
                <MenuItem value="in_progress">Devam Eden</MenuItem>
                <MenuItem value="completed">Tamamlanan</MenuItem>
              </Select>
            </FormControl>
          ) : (
            <Chip
              label={todo.status}
              color={todo.status === 'completed' ? 'success' : todo.status === 'in_progress' ? 'warning' : 'default'}
            />
          )}
        </Box>

        <Box sx={{ display: 'flex', gap: 2, mb: 2 }}>
          <Typography variant="subtitle1">Öncelik</Typography>
          {editing ? (
            <FormControl>
              <InputLabel>Öncelik</InputLabel>
              <Select
                name="priority"
                value={formData.priority}
                onChange={handleInputChange}
              >
                <MenuItem value="low">Düşük</MenuItem>
                <MenuItem value="medium">Orta</MenuItem>
                <MenuItem value="high">Yüksek</MenuItem>
              </Select>
            </FormControl>
          ) : (
            <Chip
              label={todo.priority}
              color={todo.priority === 'high' ? 'error' : todo.priority === 'medium' ? 'warning' : 'default'}
            />
          )}
        </Box>

        <Box sx={{ display: 'flex', gap: 2, mb: 2 }}>
          <Typography variant="subtitle1">Bitiş Tarihi</Typography>
          {editing ? (
            <TextField
              type="date"
              name="due_date"
              value={formData.due_date}
              onChange={handleInputChange}
              margin="none"
            />
          ) : (
            <Typography>{todo.due_date ? new Date(todo.due_date).toLocaleDateString() : '-'}</Typography>
          )}
        </Box>

        <Box sx={{ display: 'flex', gap: 2, mt: 2 }}>
          {editing ? (
            <>
              <Button
                variant="contained"
                color="primary"
                startIcon={<Save />}
                onClick={handleSave}
              >
                Kaydet
              </Button>
              <Button
                variant="outlined"
                startIcon={<Cancel />}
                onClick={handleCancel}
              >
                İptal
              </Button>
            </>
          ) : (
            <Button
              variant="contained"
              color="primary"
              startIcon={<Edit />}
              onClick={() => setEditing(true)}
            >
              Düzenle
            </Button>
          )}
          <IconButton
            color="error"
            onClick={() => setDeleteDialogOpen(true)}
          >
            <Delete />
          </IconButton>
        </Box>
      </Paper>

      <Dialog open={deleteDialogOpen} onClose={() => setDeleteDialogOpen(false)}>
        <DialogTitle>Todo Sil</DialogTitle>
        <DialogContent>
          Bu todo'yu silmek istediğinizden emin misiniz?
        </DialogContent>
        <DialogActions>
          <Button onClick={() => setDeleteDialogOpen(false)}>İptal</Button>
          <Button onClick={handleDelete} color="error">Sil</Button>
        </DialogActions>
      </Dialog>
    </Box>
  );
};

export default TodoDetail;
