import { yupResolver } from "@hookform/resolvers/yup";
import { Checkbox, FormControlLabel, TextField } from "@material-ui/core";
import { useSnackbar } from "notistack";
import React, { useEffect, useState } from "react";
import { Controller, useForm } from "react-hook-form";
import { useHistory, useParams } from "react-router-dom";
import BaseForm from "../../components/Forms/BaseForm";
import SubmitActions from "../../components/Forms/SubmitActions";
import CategoryResource from "../../http/CategoryResource";
import { Category } from "../../types/models";
import { useIsMountedRef } from "../../utils";
import { yup } from "../../utils/yup";

const categoryValidation = yup.object().shape({
  name: yup.string().label("nome").required(),
  is_active: yup.boolean().default(true),
});

const CategoriesForm = () => {
  const {
    register,
    handleSubmit,
    getValues,
    errors,
    reset,
    control,
    trigger,
  } = useForm({
    resolver: yupResolver(categoryValidation),
  });

  const isMountedRef = useIsMountedRef();
  const history = useHistory();
  const snackbar = useSnackbar();
  const { id } = useParams<{ id: string }>();
  const [loading, setLoading] = useState<boolean>(false);
  const [category, setCategory] = useState<Category | null>(null);

  useEffect(() => {
    register({ name: "is_active" });
  }, [register]);

  useEffect(() => {
    if (!id) {
      return;
    }

    (async () => {
      setLoading(true);
      try {
        const {
          data: { data },
        } = await CategoryResource.get(id);
        if (isMountedRef) {
          setCategory(data);
          reset(data as any);
        }
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

  const onSave = async () => {
    const isValid = await trigger();
    if (isValid) {
      onSubmit(getValues());
    }
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
      <SubmitActions disabled={loading} handleSave={onSave}></SubmitActions>
    </BaseForm>
  );
};

export default CategoriesForm;
