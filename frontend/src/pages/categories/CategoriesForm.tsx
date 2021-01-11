import React, { useEffect, useState } from "react";
import {
  TextField,
  FormControlLabel,
  Checkbox,
  Box,
  Button,
  ButtonProps,
  makeStyles,
  Theme,
} from "@material-ui/core";
import { Controller, useForm } from "react-hook-form";
import CategoryResource from "../../http/CategoryResource";
import { yupResolver } from "@hookform/resolvers/yup";
import { yup } from "../../utils/yup";
import { useHistory, useParams } from "react-router-dom";
import { Category } from "../../types/models";
import { useSnackbar } from "notistack";

const useStyles = makeStyles((theme: Theme) => ({
  submit: {
    margin: theme.spacing(1),
  },
}));

const categoryValidation = yup.object().shape({
  name: yup.string().label("nome").required(),
  is_active: yup.boolean().default(true),
});

const CategoriesForm = () => {
  const classes = useStyles();
  const history = useHistory();
  const snackbar = useSnackbar();

  const { id } = useParams<{ id: string }>();
  const [category, setCategory] = useState<Category | null>(null);
  const [loading, setLoading] = useState<boolean>(false);

  const { register, handleSubmit, getValues, errors, reset, control } = useForm(
    {
      resolver: yupResolver(categoryValidation),
    }
  );

  const buttonProps: ButtonProps = {
    className: classes.submit,
    color: 'secondary',
    variant: "contained",
    disabled: loading,
  };

  useEffect(() => {
    register({ name: "is_active" });
  }, [register]);

  useEffect(() => {
    if (!id) {
      return;
    }
    setLoading(true);
    CategoryResource.get(id)
      .then(({ data: { data } }) => {
        setCategory(data);
        reset(data as any);
      })
      .finally(() => {
        setLoading(false);
      });
  }, [id, reset]);

  const onSubmit = async (formData: any, event?: React.BaseSyntheticEvent) => {
    setLoading(true);
    try {
      const {
        data: { data },
      } = !category
        ? await CategoryResource.create(formData)
        : await CategoryResource.update(category.id, formData);
      snackbar.enqueueSnackbar("Categoria salva com sucesso", {
        variant: "success",
      });
      if (event) {
        id
          ? history.replace(`/categories/${data.id}/edit`)
          : history.push(`/categories/${data.id}/edit`);
      } else {
        history.push("/categories");
      }
    } catch (error) {
      snackbar.enqueueSnackbar("Error ao tentar salvar categoria", {
        variant: "error",
      });
    } finally {
      setLoading(false);
    }
  };

  const onSave = async () => onSubmit(getValues());

  return (
    <form onSubmit={handleSubmit(onSubmit)}>
      <TextField
        inputRef={register}
        error={errors.name !== undefined}
        helperText={errors.name?.message}
        disabled={loading}
        name="name"
        label="Nome"
        variant="outlined"
        fullWidth
        InputLabelProps={{ shrink: true }}
      />
      <TextField
        inputRef={register}
        disabled={loading}
        name="description"
        label="Descrição"
        variant="outlined"
        rows="4"
        margin="normal"
        multiline
        fullWidth
        InputLabelProps={{ shrink: true }}
      />
      <Controller
        control={control}
        defaultValue={category ? category.is_active : true}
        disabled={loading}
        name="is_active"
        render={({ name, value, onBlur, onChange, ref }) => {
          return (
            <FormControlLabel
              disabled={loading}
              control={
                <Checkbox
                  name={name}
                  onBlur={onBlur}
                  checked={value}
                  inputRef={ref}
                  color="primary"
                  onChange={(e) => onChange(e.target.checked)}
                />
              }
              label="Ativo?"
              labelPlacement="end"
            />
          );
        }}
      />

      <Box dir="rtl">
        <Button color="primary" {...buttonProps} onClick={onSave}>
          Salvar
        </Button>
        <Button color="primary" {...buttonProps} type="submit">
          Salvar e continuar editado
        </Button>
      </Box>
    </form>
  );
};

export default CategoriesForm;
