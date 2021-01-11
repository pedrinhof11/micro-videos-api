import {
  Box,
  Button,
  ButtonProps,
  makeStyles,
  MenuItem,
  TextField,
  Theme,
} from "@material-ui/core";
import React, { useEffect, useState } from "react";
import { useForm } from "react-hook-form";
import CategoryResource from "../../http/CategoryResource";
import GenreResource from "../../http/GenreResource";
import { Category } from "../../types/models";

const useStyles = makeStyles((theme: Theme) => ({
  submit: {
    margin: theme.spacing(1),
  },
}));

const GenresForm = () => {
  const classes = useStyles();
  const [categories, setCategories] = useState<Category[]>([]);

  const buttonProps: ButtonProps = {
    className: classes.submit,
    color: "secondary",
    variant: "contained",
  };
  const { register, handleSubmit, getValues, setValue, watch } = useForm({
    defaultValues: {
      categories_id: [],
    },
  });

  const fetchCategories = async () => {
    const {
      data: { data },
    } = await CategoryResource.list();
    setCategories(data);
  };

  useEffect(() => {
    register({ name: "categories_id" });
  }, [register]);

  useEffect(() => {
    fetchCategories();
  }, []);

  const onSubmit = async (formData: any, event?: any) => {
    try {
      const { data } = await GenreResource.create(formData);
      console.log(data);
    } finally {
    }
  };

  const onSave = async () => onSubmit(getValues(), null);

  const handleChangeCategories = (event: any) => {
    setValue("categories_id", event.target.value);
  };

  return (
    <form onSubmit={handleSubmit(onSubmit)}>
      <TextField
        inputRef={register}
        name="name"
        label="Nome"
        variant="outlined"
        fullWidth
      />
      <TextField
        select
        value={watch("categories_id")}
        name="categories_id"
        label="Categorias"
        variant="outlined"
        rows="4"
        margin="normal"
        multiline
        fullWidth
        onChange={handleChangeCategories}
        SelectProps={{
          multiple: true,
        }}
      >
        <MenuItem value="">
          <em>Selecione uma Categoria</em>
        </MenuItem>
        {categories.map((category, key) => (
          <MenuItem key={key} value={category.id}>
            {category.name}
          </MenuItem>
        ))}
      </TextField>
      <Box dir="rtl">
        <Button {...buttonProps} onClick={onSave}>
          Salvar
        </Button>
        <Button {...buttonProps} type="submit">
          Salvar e continuar editado
        </Button>
      </Box>
    </form>
  );
};

export default GenresForm;
