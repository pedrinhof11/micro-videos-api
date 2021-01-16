import { yupResolver } from "@hookform/resolvers/yup";
import { MenuItem, TextField } from "@material-ui/core";
import { useSnackbar } from "notistack";
import React, { useEffect, useState } from "react";
import { useForm } from "react-hook-form";
import { useHistory, useParams } from "react-router-dom";
import BaseForm from "../../components/Forms/BaseForm";
import SubmitActions from "../../components/Forms/SubmitActions";
import CategoryResource from "../../http/CategoryResource";
import GenreResource from "../../http/GenreResource";
import { Category, Genre } from "../../types/models";
import { useIsMountedRef } from "../../utils";
import { yup } from "../../utils/yup";

const validationSchema = yup.object().shape({
  name: yup.string().label("nome").required(),
  categories_id: yup.array(yup.string()).label("Categorias").required(),
});

const GenresForm = () => {
  const {
    register,
    handleSubmit,
    getValues,
    setValue,
    watch,
    errors,
    reset,
    trigger,
  } = useForm({
    resolver: yupResolver(validationSchema),
  });

  const isMountedRef = useIsMountedRef();
  const history = useHistory();
  const snackbar = useSnackbar();
  const { id } = useParams<{ id: string }>();
  const [loading, setLoading] = useState<boolean>(false);
  const [genre, setGenre] = useState<Genre | null>(null);
  const [categories, setCategories] = useState<Category[]>([]);

  useEffect(() => {
    register({ name: "categories_id" });
  }, [register]);

  useEffect(() => {
    async function getGenre() {
      const {
        data: { data },
      } = await GenreResource.get(id);
      if (isMountedRef.current) {
        setGenre(data);
        reset({
          ...data,
          categories_id: data.categories.map((category: any) => category.id),
        } as any);
      }
    }

    async function fetchCategories() {
      const {
        data: { data },
      } = await CategoryResource.list();
      if (isMountedRef.current) {
        setCategories(data);
      }
    }

    (async () => {
      setLoading(true);
      const promisses = [fetchCategories()];
      if (id) {
        promisses.push(getGenre());
      }
      try {
        await Promise.all(promisses);
      } catch (error) {
        snackbar.enqueueSnackbar("Não foi possivel carregar as informações", {
          variant: "error",
        });
      } finally {
        setLoading(false);
      }
    })();
  }, [id, reset, snackbar, isMountedRef]);

  const onSubmit = async (formData: any, event?: React.BaseSyntheticEvent) => {
    setLoading(true);
    try {
      const {
        data: { data },
      } = !genre
        ? await GenreResource.create(formData)
        : await GenreResource.update(genre.id, formData);
      snackbar.enqueueSnackbar("Gênero salvo com sucesso", {
        variant: "success",
      });
      if (event) {
        id
          ? history.replace(`/genres/${data.id}/edit`)
          : history.push(`/genres/${data.id}/edit`);
      } else {
        history.push("/genres");
      }
    } catch (error) {
      snackbar.enqueueSnackbar("Error ao tentar salvar Gênero", {
        variant: "error",
      });
    } finally {
      setLoading(false);
    }
  };

  const onSave = async () => {
    const isValid = await trigger();
    if (isValid) {
      onSubmit(getValues());
    }
  };

  const handleChangeCategories = (event: React.BaseSyntheticEvent) => {
    setValue("categories_id", event.target.value);
  };

  return (
    <BaseForm
      GridItemProps={{ xs: 12, md: 6 }}
      onSubmit={handleSubmit(onSubmit)}
    >
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
        select
        value={
          watch("categories_id") !== undefined ? watch("categories_id") : []
        }
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
        error={errors.categories_id !== undefined}
        helperText={errors.categories_id?.[0]?.message}
        disabled={loading}
        InputLabelProps={{ shrink: true }}
      >
        <MenuItem value="" disabled>
          <em>Selecione uma Categoria</em>
        </MenuItem>
        {categories.map((category, key) => (
          <MenuItem key={key} value={category.id}>
            {category.name}
          </MenuItem>
        ))}
      </TextField>
      <SubmitActions disabled={loading} handleSave={onSave}></SubmitActions>
    </BaseForm>
  );
};

export default GenresForm;
