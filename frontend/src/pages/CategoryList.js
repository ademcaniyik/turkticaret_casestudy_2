import React, { useState } from 'react';
import { useSelector, useDispatch } from 'react-redux';
import { Box, Paper, Typography, TextField, Button, IconButton, Table, TableBody, TableCell, TableContainer, TableHead, TableRow, Dialog, DialogTitle, DialogContent, DialogActions, FormControl, InputLabel, Select, MenuItem, Chip } from '@mui/material';
import { Add, Edit, Delete } from '@mui/icons-material';
import { deleteCategory, updateCategory, createCategory } from '../features/categories/categoriesSlice';

const CategoryList = () => {
  const dispatch = useDispatch();
  const categories = useSelector((state) => state.categories.items);
  const [open, setOpen] = useState(false);
  const [editing, setEditing] = useState(null);
  const [formData, setFormData] = useState({
    name: '',
    description: '',
    color: '#000000',
  });

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: value,
    }));
  };

  const handleAdd = () => {
    setEditing(null);
    setFormData({
      name: '',
      description: '',
      color: '#000000',
    });
    setOpen(true);
  };

  const handleEdit = (category) => {
    setEditing(category.id);
    setFormData({
      name: category.name,
      description: category.description,
      color: category.color,
    });
    setOpen(true);
  };

  const handleSave = () => {
    if (editing) {
      dispatch(updateCategory({
        id: editing,
        ...formData,
      }));
    } else {
      dispatch(createCategory(formData));
    }
    setOpen(false);
  };

  const handleDelete = (id) => {
    dispatch(deleteCategory(id));
  };

  return (
    <Box sx={{ p: 3 }}>
      <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', mb: 3 }}>
        <Typography variant="h4">Kategoriler</Typography>
        <Button
          variant="contained"
          color="primary"
          startIcon={<Add />}
          onClick={handleAdd}
        >
          Yeni Kategori
        </Button>
      </Box>

      <TableContainer component={Paper}>
        <Table>
          <TableHead>
            <TableRow>
              <TableCell>Ad</TableCell>
              <TableCell>Açıklama</TableCell>
              <TableCell>Renk</TableCell>
              <TableCell>Todo Sayısı</TableCell>
              <TableCell>İşlemler</TableCell>
            </TableRow>
          </TableHead>
          <TableBody>
            {categories.map((category) => (
              <TableRow key={category.id}>
                <TableCell>{category.name}</TableCell>
                <TableCell>{category.description}</TableCell>
                <TableCell>
                  <Chip
                    label={category.color}
                    style={{ backgroundColor: category.color }}
                  />
                </TableCell>
                <TableCell>{category.todo_count || 0}</TableCell>
                <TableCell>
                  <IconButton onClick={() => handleEdit(category)}>
                    <Edit />
                  </IconButton>
                  <IconButton onClick={() => handleDelete(category.id)} color="error">
                    <Delete />
                  </IconButton>
                </TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </TableContainer>

      <Dialog open={open} onClose={() => setOpen(false)}>
        <DialogTitle>
          {editing ? 'Kategori Düzenle' : 'Yeni Kategori'}
        </DialogTitle>
        <DialogContent>
          <Box sx={{ mt: 2 }}>
            <TextField
              fullWidth
              label="Ad"
              name="name"
              value={formData.name}
              onChange={handleInputChange}
              margin="normal"
            />
            <TextField
              fullWidth
              label="Açıklama"
              name="description"
              value={formData.description}
              onChange={handleInputChange}
              margin="normal"
            />
            <FormControl fullWidth margin="normal">
              <InputLabel>Renk</InputLabel>
              <Select
                name="color"
                value={formData.color}
                onChange={handleInputChange}
              >
                <MenuItem value="#000000">Siyah</MenuItem>
                <MenuItem value="#FF0000">Kırmızı</MenuItem>
                <MenuItem value="#00FF00">Yeşil</MenuItem>
                <MenuItem value="#0000FF">Mavi</MenuItem>
                <MenuItem value="#FFFF00">Sarı</MenuItem>
                <MenuItem value="#FF00FF">Magenta</MenuItem>
                <MenuItem value="#00FFFF">Sıyan</MenuItem>
              </Select>
            </FormControl>
          </Box>
        </DialogContent>
        <DialogActions>
          <Button onClick={() => setOpen(false)}>İptal</Button>
          <Button onClick={handleSave} color="primary">
            Kaydet
          </Button>
        </DialogActions>
      </Dialog>
    </Box>
  );
};

export default CategoryList;
